<?php

namespace App\Services;

use App\Models\Server;
use App\Models\Alert;
use App\Models\AlertRule;
use App\Models\ServerMetric;
use Illuminate\Support\Facades\Mail;
use Exception;

class AlertService
{
    /**
     * Check alert rules and create alerts if thresholds are exceeded
     */
    public function checkAlerts(Server $server, ServerMetric $metric): void
    {
        $rules = AlertRule::where('server_id', $server->id)
            ->where('is_enabled', true)
            ->get();

        foreach ($rules as $rule) {
            $this->evaluateRule($server, $rule, $metric);
        }
    }

    /**
     * Evaluate a single alert rule
     */
    private function evaluateRule(Server $server, AlertRule $rule, ServerMetric $metric): void
    {
        $metricValue = $metric->{$rule->metric_type};
        $threshold = $rule->threshold;

        $triggered = match($rule->operator) {
            '>' => $metricValue > $threshold,
            '<' => $metricValue < $threshold,
            '=' => $metricValue == $threshold,
            '!=' => $metricValue != $threshold,
            default => false,
        };

        if ($triggered) {
            // Check if alert already exists and is unresolved
            $existingAlert = Alert::where('server_id', $server->id)
                ->where('alert_rule_id', $rule->id)
                ->where('is_resolved', false)
                ->latest()
                ->first();

            // Only create if it's new or outside the duration window
            if (!$existingAlert || now()->diffInMinutes($existingAlert->created_at) > $rule->duration_minutes) {
                $this->createAlert($server, $rule, $metricValue);
            }
        }
    }

    /**
     * Create a new alert
     */
    private function createAlert(Server $server, AlertRule $rule, $metricValue): void
    {
        $alert = Alert::create([
            'server_id' => $server->id,
            'type' => Alert::TYPE_CUSTOM,
            'severity' => $rule->severity,
            'title' => $rule->name,
            'message' => "{$rule->name}: {$metricValue} {$rule->operator} {$rule->threshold}",
            'metric_type' => $rule->metric_type,
            'metric_value' => $metricValue,
            'threshold' => $rule->threshold,
        ]);

        $this->sendNotifications($alert, $rule);
    }

    /**
     * Send alert notifications
     */
    public function sendNotifications(Alert $alert, AlertRule $rule): void
    {
        if ($rule->notify_email) {
            $this->sendEmailNotification($alert, $rule);
        }

        if ($rule->notify_webhook_url) {
            $this->sendWebhookNotification($alert, $rule);
        }
    }

    /**
     * Send email notification
     */
    private function sendEmailNotification(Alert $alert, AlertRule $rule): void
    {
        try {
            // TODO: Implement email sending
            // Mail::send(new AlertNotification($alert, $rule));
        } catch (Exception $e) {
            \Log::error('Failed to send alert email: ' . $e->getMessage());
        }
    }

    /**
     * Send webhook notification
     */
    private function sendWebhookNotification(Alert $alert, AlertRule $rule): void
    {
        try {
            $payload = [
                'alert_id' => $alert->id,
                'server_id' => $alert->server->id,
                'title' => $alert->title,
                'message' => $alert->message,
                'severity' => $alert->severity,
                'metric_value' => $alert->metric_value,
                'threshold' => $alert->threshold,
                'created_at' => $alert->created_at,
            ];

            \Http::post($rule->notify_webhook_url, $payload);
        } catch (Exception $e) {
            \Log::error('Failed to send webhook notification: ' . $e->getMessage());
        }
    }
}
