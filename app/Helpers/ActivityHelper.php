<?php

use App\Models\ActivityLog;

if (!function_exists('logActivity')) {
    /**
     * Helper global untuk mencatat log aktivitas user.
     *
     * @param string $action Nama aksi/tindakan (contoh: 'Login', 'Checkout Transaksi')
     * @param string|null $description Dekripsi detail opsional
     * @return \App\Models\ActivityLog|null
     */
    function logActivity($action, $description = null)
    {
        
        if (auth()->check()) {
            return ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => $action,
                'description' => $description
            ]);
        }
        
        return null;
    }
}
