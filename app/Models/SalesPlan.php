<?php

namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\SoftDeletes;

    class SalesPlan extends Model
    {
        use HasFactory;
        use SoftDeletes; // Tambahkan ini
        protected $table = 'salesplans'; // Specify the table name if it differs from the model name
        protected $fillable = [
            'data_id',
            'fu1_hasil', 'fu1_tindak_lanjut', 'fu1_wa', 'fu1_telp', 'fu1_at',
            'fu2_hasil', 'fu2_tindak_lanjut', 'fu2_wa', 'fu2_telp', 'fu2_at',
            'fu3_hasil', 'fu3_tindak_lanjut', 'fu3_wa', 'fu3_telp', 'fu3_at',
            'fu4_hasil', 'fu4_tindak_lanjut', 'fu4_wa', 'fu4_telp', 'fu4_at',
            'fu5_hasil', 'fu5_tindak_lanjut', 'fu5_wa', 'fu5_telp', 'fu5_at',
            'fu6_hasil', 'fu6_tindak_lanjut', 'fu6_wa', 'fu6_telp', 'fu6_at',
            'fu7_hasil', 'fu7_tindak_lanjut', 'fu7_wa', 'fu7_telp', 'fu7_at',
            'fu8_hasil', 'fu8_tindak_lanjut', 'fu8_wa', 'fu8_telp', 'fu8_at',
            'fu9_hasil', 'fu9_tindak_lanjut', 'fu9_wa', 'fu9_telp', 'fu9_at',
            'fu10_hasil', 'fu10_tindak_lanjut', 'fu10_wa', 'fu10_telp', 'fu10_at',
            'fu11_hasil', 'fu11_at',
            'fu11_tindak_lanjut',
            'fu12_hasil', 'fu12_at',
            'fu12_tindak_lanjut',
            'fu1_done', 'fu2_done', 'fu3_done', 'fu4_done', 'fu5_done', 'fu6_done', 
            'fu7_done', 'fu8_done', 'fu9_done', 'fu10_done', 'fu11_done', 'fu12_done',
            'keterangan',
            'nominal',
            'kebutuhan',
            'created_by',
            'level',
            'komentar_atasan',
            'fu_history',
            'tanggal_closing',
            'selected_months'
        ];
        protected $casts = [
            'status' => 'string', // Cast status to string
            'closing_paket' => 'boolean',
            'fu_history' => 'array',
            'fu1_at' => 'datetime',
            'fu2_at' => 'datetime',
            'fu3_at' => 'datetime',
            'fu4_at' => 'datetime',
            'fu5_at' => 'datetime',
            'fu6_at' => 'datetime',
            'fu7_at' => 'datetime',
            'fu8_at' => 'datetime',
            'fu9_at' => 'datetime',
            'fu10_at' => 'datetime',
            'fu11_at' => 'datetime',
            'fu12_at' => 'datetime',
            'fu1_rtl_at' => 'datetime',
            'fu2_rtl_at' => 'datetime',
            'fu3_rtl_at' => 'datetime',
            'fu4_rtl_at' => 'datetime',
            'fu5_rtl_at' => 'datetime',
            'fu6_rtl_at' => 'datetime',
            'fu7_rtl_at' => 'datetime',
            'fu8_rtl_at' => 'datetime',
            'fu9_rtl_at' => 'datetime',
            'fu10_rtl_at' => 'datetime',
            'fu11_rtl_at' => 'datetime',
            'fu12_rtl_at' => 'datetime',
            'fu1_done' => 'boolean',
            'fu2_done' => 'boolean',
            'fu3_done' => 'boolean',
            'fu4_done' => 'boolean',
            'fu5_done' => 'boolean',
            'fu6_done' => 'boolean',
            'fu7_done' => 'boolean',
            'fu8_done' => 'boolean',
            'fu9_done' => 'boolean',
            'fu10_done' => 'boolean',
            'fu11_done' => 'boolean',
            'fu12_done' => 'boolean',
            'fu1_wa' => 'boolean',
            'fu2_wa' => 'boolean',
            'fu3_wa' => 'boolean',
            'fu4_wa' => 'boolean',
            'fu5_wa' => 'boolean',
            'fu6_wa' => 'boolean',
            'fu7_wa' => 'boolean',
            'fu8_wa' => 'boolean',
            'fu9_wa' => 'boolean',
            'fu10_wa' => 'boolean',
            'fu1_telp' => 'boolean',
            'fu2_telp' => 'boolean',
            'fu3_telp' => 'boolean',
            'fu4_telp' => 'boolean',
            'fu5_telp' => 'boolean',
            'fu6_telp' => 'boolean',
            'fu7_telp' => 'boolean',
            'fu8_telp' => 'boolean',
            'fu9_telp' => 'boolean',
            'fu10_telp' => 'boolean',
            'tanggal_closing' => 'date',
            'selected_months' => 'array',
        ];


        /**
         * Relasi dengan model data.
         */
        public function data()
        {
            return $this->belongsTo(Data::class, 'data_id');
        }
        public function kelas()
        {
            return $this->belongsTo(Kelas::class, 'kelas_id');
        }
        
        // app/Models/SalesPlan.php
    public function createdBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }


        public function getCreatedByNameAttribute()
        {
            if ($this->createdBy) {
                return $this->createdBy->name;
            }

            if (empty($this->created_by)) {
                return '-';
            }

            $user = \App\Models\User::find($this->created_by);
            if ($user) {
                return $user->name;
            }

            return 'User #' . $this->created_by;
        }

        public function pesertaSmi()
        {
            return $this->hasOne(PesertaSmi::class, 'sales_plan_id');
        }
    }
