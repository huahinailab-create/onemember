<?php

namespace App\Services;

use App\Models\Merchant;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportService
{
    // UTF-8 BOM for Excel compatibility
    const BOM = "\xEF\xBB\xBF";

    // -----------------------------------------------------------------------
    // Members
    // -----------------------------------------------------------------------

    public function streamMembers(Merchant $merchant): StreamedResponse
    {
        $filename = $this->filename('members', $merchant);
        $headers  = [
            __('data.export_col_member_code'),
            __('data.export_col_name'),
            __('data.export_col_phone'),
            __('data.export_col_email'),
            __('data.export_col_birthday'),
            __('data.export_col_status'),
            __('data.export_col_total_points'),
            __('data.export_col_lifetime_points'),
            __('data.export_col_notes'),
            __('data.export_col_joined_at'),
        ];

        return $this->stream($filename, $headers, function ($handle) use ($merchant) {
            $merchant->members()
                ->withTrashed(false)
                ->orderBy('id')
                ->chunk(500, function ($members) use ($handle) {
                    foreach ($members as $m) {
                        fputcsv($handle, [
                            $m->member_code,
                            $m->name,
                            $m->phone,
                            $m->email,
                            $m->birthday?->format('Y-m-d'),
                            $m->status->value,
                            $m->total_points,
                            $m->lifetime_points,
                            $m->notes,
                            $m->joined_at?->format('Y-m-d H:i:s'),
                        ]);
                    }
                });
        });
    }

    // -----------------------------------------------------------------------
    // Campaigns (loyalty_programs)
    // -----------------------------------------------------------------------

    public function streamCampaigns(Merchant $merchant): StreamedResponse
    {
        $filename = $this->filename('campaigns', $merchant);
        $headers  = [
            'ID',
            __('data.export_col_name'),
            __('data.export_col_type'),
            __('data.export_col_status'),
            __('data.export_col_description'),
            __('data.export_col_created_at'),
        ];

        return $this->stream($filename, $headers, function ($handle) use ($merchant) {
            $merchant->loyaltyPrograms()
                ->withTrashed(false)
                ->orderBy('id')
                ->chunk(500, function ($programs) use ($handle) {
                    foreach ($programs as $p) {
                        fputcsv($handle, [
                            $p->id,
                            $p->name,
                            $p->type->value,
                            $p->status->value,
                            $p->description,
                            $p->created_at?->format('Y-m-d H:i:s'),
                        ]);
                    }
                });
        });
    }

    // -----------------------------------------------------------------------
    // Rewards
    // -----------------------------------------------------------------------

    public function streamRewards(Merchant $merchant): StreamedResponse
    {
        $filename = $this->filename('rewards', $merchant);
        $headers  = [
            'ID',
            __('data.export_col_campaign'),
            __('data.export_col_name'),
            __('data.export_col_type'),
            __('data.export_col_points_required'),
            __('data.export_col_quantity_available'),
            __('data.export_col_quantity_redeemed'),
            __('data.export_col_status'),
            __('data.export_col_created_at'),
        ];

        return $this->stream($filename, $headers, function ($handle) use ($merchant) {
            $merchant->rewards()
                ->withTrashed(false)
                ->with('loyaltyProgram:id,name')
                ->orderBy('id')
                ->chunk(500, function ($rewards) use ($handle) {
                    foreach ($rewards as $r) {
                        fputcsv($handle, [
                            $r->id,
                            $r->loyaltyProgram?->name,
                            $r->name,
                            $r->type->value,
                            $r->points_required,
                            $r->quantity_available,
                            $r->quantity_redeemed ?? 0,
                            $r->status->value,
                            $r->created_at?->format('Y-m-d H:i:s'),
                        ]);
                    }
                });
        });
    }

    // -----------------------------------------------------------------------
    // Purchases (earn transactions)
    // -----------------------------------------------------------------------

    public function streamPurchases(Merchant $merchant): StreamedResponse
    {
        $filename = $this->filename('purchases', $merchant);
        $headers  = [
            'ID',
            __('data.export_col_member_code'),
            __('data.export_col_member_name'),
            __('data.export_col_campaign'),
            __('data.export_col_points'),
            __('data.export_col_purchase_amount'),
            __('data.export_col_invoice_number'),
            __('data.export_col_note'),
            __('data.export_col_created_at'),
        ];

        return $this->stream($filename, $headers, function ($handle) use ($merchant) {
            $merchant->transactions()
                ->where('type', 'earn')
                ->with(['member:id,name,member_code', 'loyaltyProgram:id,name'])
                ->orderBy('id')
                ->chunk(500, function ($txns) use ($handle) {
                    foreach ($txns as $t) {
                        fputcsv($handle, [
                            $t->id,
                            $t->member?->member_code,
                            $t->member?->name,
                            $t->loyaltyProgram?->name,
                            $t->points,
                            $t->purchase_amount,
                            $t->invoice_number,
                            $t->note,
                            $t->created_at?->format('Y-m-d H:i:s'),
                        ]);
                    }
                });
        });
    }

    // -----------------------------------------------------------------------
    // Redemptions
    // -----------------------------------------------------------------------

    public function streamRedemptions(Merchant $merchant): StreamedResponse
    {
        $filename = $this->filename('redemptions', $merchant);
        $headers  = [
            'ID',
            __('data.export_col_member_code'),
            __('data.export_col_member_name'),
            __('data.export_col_reward'),
            __('data.export_col_points_used'),
            __('data.export_col_code'),
            __('data.export_col_status'),
            __('data.export_col_redeemed_at'),
            __('data.export_col_expires_at'),
        ];

        return $this->stream($filename, $headers, function ($handle) use ($merchant) {
            $merchant->redemptions()
                ->with(['member:id,name,member_code', 'reward:id,name'])
                ->orderBy('id')
                ->chunk(500, function ($redemptions) use ($handle) {
                    foreach ($redemptions as $r) {
                        fputcsv($handle, [
                            $r->id,
                            $r->member?->member_code,
                            $r->member?->name,
                            $r->reward?->name,
                            $r->points_used,
                            $r->code,
                            $r->status->value,
                            $r->redeemed_at?->format('Y-m-d H:i:s'),
                            $r->expires_at?->format('Y-m-d H:i:s'),
                        ]);
                    }
                });
        });
    }

    // -----------------------------------------------------------------------
    // Internal helpers
    // -----------------------------------------------------------------------

    private function stream(string $filename, array $headers, callable $writer): StreamedResponse
    {
        return response()->stream(function () use ($headers, $writer) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM
            fwrite($handle, self::BOM);

            // Header row
            fputcsv($handle, $headers);

            // Data rows via callback
            $writer($handle);

            fclose($handle);
        }, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'no-cache, no-store, must-revalidate',
            'Pragma'              => 'no-cache',
            'Expires'             => '0',
        ]);
    }

    private function filename(string $type, Merchant $merchant): string
    {
        $slug = Str::slug($merchant->name ?? 'merchant');
        $ts   = now()->format('Ymd_His');
        return "onemember_{$type}_{$slug}_{$ts}.csv";
    }
}
