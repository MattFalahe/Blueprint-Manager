<?php

namespace BlueprintManager\Services;

use BlueprintManager\Models\WebhookConfig;
use BlueprintManager\Models\BlueprintRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DiscordNotificationService
{
    /**
     * Send notification when a request is created
     */
    public function notifyRequestCreated(BlueprintRequest $request)
    {
        $this->sendNotification($request, 'created');
    }

    /**
     * Send notification when a request is approved
     */
    public function notifyRequestApproved(BlueprintRequest $request, $approverName, $notes = null)
    {
        $this->sendNotification($request, 'approved', [
            'responder_name' => $approverName,
            'notes' => $notes
        ]);
    }

    /**
     * Send notification when a request is rejected
     */
    public function notifyRequestRejected(BlueprintRequest $request, $rejectorName, $notes = null)
    {
        $this->sendNotification($request, 'rejected', [
            'responder_name' => $rejectorName,
            'notes' => $notes
        ]);
    }

    /**
     * Send notification when a request is fulfilled
     */
    public function notifyRequestFulfilled(BlueprintRequest $request, $fulfillerName, $notes = null)
    {
        $this->sendNotification($request, 'fulfilled', [
            'responder_name' => $fulfillerName,
            'notes' => $notes
        ]);
    }

    /**
     * Main notification method
     */
    protected function sendNotification(BlueprintRequest $request, string $action, array $additionalData = [])
    {
        try {
            // Load relationships
            $request->load(['character', 'corporation', 'blueprintType', 'approver', 'rejector', 'fulfiller']);

            // Get applicable webhooks
            $webhooks = $this->getApplicableWebhooks($request, $action);

            foreach ($webhooks as $webhook) {
                try {
                    $embed = $this->buildEmbed($request, $action, $additionalData);
                    $payload = ['embeds' => [$embed]];
                    
                    // Add role ping if configured for this action
                    $rolePing = $this->getRolePing($webhook, $action);
                    if ($rolePing) {
                        $payload['content'] = $rolePing;
                    }
                    
                    $response = Http::timeout(10)->post($webhook->webhook_url, $payload);

                    if (!$response->successful()) {
                        Log::warning('Discord webhook failed', [
                            'webhook_id' => $webhook->id,
                            'status' => $response->status(),
                            'response' => $response->body()
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('Discord webhook error', [
                        'webhook_id' => $webhook->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Discord notification error', [
                'action' => $action,
                'request_id' => $request->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get webhooks that should receive this notification
     */
    protected function getApplicableWebhooks(BlueprintRequest $request, string $action)
    {
        $notifyField = 'notify_' . $action;

        return WebhookConfig::where('enabled', true)
            ->where($notifyField, true)
            ->where(function ($query) use ($request) {
                $query->whereNull('corporation_id')
                    ->orWhere('corporation_id', $request->corporation_id);
            })
            ->get();
    }

    /**
     * Get role ping for the action if configured
     */
    protected function getRolePing(WebhookConfig $webhook, string $action): ?string
    {
        $roleField = 'ping_role_' . $action;
        $roleId = $webhook->$roleField;

        if (!$roleId) {
            return null;
        }

        // Discord role mention format: <@&role_id>
        return "<@&{$roleId}>";
    }

    /**
     * Build Discord embed
     */
    protected function buildEmbed(BlueprintRequest $request, string $action, array $additionalData = [])
    {
        $embed = [
            'title' => $this->getTitle($action),
            'color' => $this->getColor($action),
            'timestamp' => now()->toIso8601String(),
            'fields' => []
        ];

        // Blueprint Information
        $blueprintValue = $request->blueprintType->typeName ?? 'Unknown';
        
        // Add quantity and runs to blueprint line for compact display
        $quantityText = $request->quantity . ($request->runs ? ' × ' . $request->runs . ' run' . ($request->runs > 1 ? 's' : '') : '');
        
        $embed['fields'][] = [
            'name' => 'Blueprint',
            'value' => $blueprintValue,
            'inline' => false
        ];

        $embed['fields'][] = [
            'name' => 'Quantity',
            'value' => $quantityText,
            'inline' => false
        ];

        // Requested By and Corporation on same line
        $embed['fields'][] = [
            'name' => 'Requested by',
            'value' => $request->character->name ?? 'Unknown',
            'inline' => true
        ];

        $embed['fields'][] = [
            'name' => 'Corporation',
            'value' => $request->corporation->name ?? 'Unknown',
            'inline' => true
        ];

        // Request Notes
        if ($request->notes) {
            $embed['fields'][] = [
                'name' => 'Notes',
                'value' => $this->truncate($request->notes, 1024),
                'inline' => false
            ];
        }

        // Action-specific fields (for approved/rejected/fulfilled)
        if (isset($additionalData['responder_name'])) {
            $actionLabel = $this->getResponderLabel($action);
            $embed['fields'][] = [
                'name' => $actionLabel,
                'value' => $additionalData['responder_name'],
                'inline' => false
            ];
        }

        // Response Notes (keep emoji here as it's important feedback)
        if (isset($additionalData['notes']) && $additionalData['notes']) {
            $embed['fields'][] = [
                'name' => '💬 Response',
                'value' => $this->truncate($additionalData['notes'], 1024),
                'inline' => false
            ];
        }

        // Clean, compact footer
        $embed['footer'] = [
            'text' => 'Request #' . $request->id . ' • ' . $request->created_at->format('M d, g:i A')
        ];

        return $embed;
    }

    /**
     * Get title based on action
     */
    protected function getTitle(string $action): string
    {
        $titles = [
            'created' => '✨ New Blueprint Request',
            'approved' => '✅ Blueprint Request Approved',
            'rejected' => '❌ Blueprint Request Rejected',
            'fulfilled' => '✨ Blueprint Request Fulfilled'
        ];

        return $titles[$action] ?? 'Blueprint Request Update';
    }

    /**
     * Get color based on action
     */
    protected function getColor(string $action): int
    {
        $colors = [
            'created' => 3447003,   // Blue
            'approved' => 5763719,  // Green
            'rejected' => 15158332, // Red
            'fulfilled' => 10181046 // Purple
        ];

        return $colors[$action] ?? 3447003;
    }

    /**
     * Get responder label based on action
     */
    protected function getResponderLabel(string $action): string
    {
        $labels = [
            'approved' => 'Approved by',
            'rejected' => 'Rejected by',
            'fulfilled' => 'Fulfilled by'
        ];

        return $labels[$action] ?? 'Processed by';
    }

    /**
     * Truncate text to specified length
     */
    protected function truncate(string $text, int $length): string
    {
        if (strlen($text) <= $length) {
            return $text;
        }

        return substr($text, 0, $length - 3) . '...';
    }
}
