<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Exception untuk error logika bisnis yang pesannya aman ditampilkan langsung ke pengguna.
 *
 * Contoh: stok tidak mencukupi, aturan pajak dilanggar, keranjang kosong.
 * Untuk error teknis (DB, koneksi, dsb), gunakan Exception/Throwable biasa
 * agar ditangkap oleh handler generik yang me-log detail dan menampilkan pesan umum.
 */
class BusinessException extends RuntimeException
{
    //
}
