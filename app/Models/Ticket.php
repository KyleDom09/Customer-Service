<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ticket_number',
        'customer_name',
        'customer_email',
        'subject',
        'sub_subject',
        'category',
        'agent_id',
        'priority',
        'status',
        'description',
        'response_minutes',
        'resolved_at',
    ];

    // Relasyon: isang ticket ay maaaring may naka-link na account (user_id)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasyon: isang ticket ay maaaring may isang naka-assign na agent
    public function agentModel()
    {
        return $this->belongsTo(Agent::class, 'agent_id');
    }

    public function communications()
    {
        return $this->hasMany(Communication::class);
    }

    // Ginagamit sa blade bilang $ticket->name
    public function getNameAttribute()
    {
        return $this->customer_name;
    }

    // Ginagamit sa blade bilang $ticket->email
    public function getEmailAttribute()
    {
        return $this->customer_email;
    }

    // Bumubuo ng initials mula sa pangalan ng customer, hal. "Juan Dela Cruz" -> "JD"
    public function getInitialsAttribute()
    {
        return $this->makeInitials($this->customer_name);
    }

    // Kulay ng avatar circle ng customer, batay sa pangalan (consistent kada refresh)
    public function getAvatarBgAttribute()
    {
        return $this->pickColor($this->customer_name);
    }

    // Pangalan ng naka-assign na agent, o "Unassigned" kung wala pa
    public function getAgentAttribute()
    {
        return $this->agentModel ? $this->agentModel->name : 'Unassigned';
    }

    // Initials ng agent, o "-" kung wala pang naka-assign
    public function getAgentInitialsAttribute()
    {
        return $this->agentModel ? $this->makeInitials($this->agentModel->name) : '-';
    }

    // Kulay ng avatar circle ng agent
    public function getAgentBgAttribute()
    {
        return $this->agentModel ? $this->pickColor($this->agentModel->name) : 'bg-slate-200 text-slate-400';
    }

    // Maganda at simpleng format ng petsa ng pagkakagawa, hal. "2 hours ago"
    public function getCreatedAttribute()
    {
        return $this->created_at ? $this->created_at->diffForHumans() : '-';
    }

    // Maganda at simpleng format ng huling pag-update
    public function getUpdatedAttribute()
    {
        return $this->updated_at ? $this->updated_at->diffForHumans() : '-';
    }

    public function messages()
    {
        return $this->hasMany(TicketMessage::class);
    }

    // Helper: kumuha ng unang letra ng unang 2 salita ng pangalan
    private function makeInitials($name)
    {
        if (!$name) {
            return '-';
        }

        $words = preg_split('/\s+/', trim($name));
        $initials = strtoupper(substr($words[0], 0, 1));

        if (count($words) > 1) {
            $initials .= strtoupper(substr($words[count($words) - 1], 0, 1));
        }

        return $initials;
    }

    // Helper: palaging parehong kulay ang mabubuo para sa parehong pangalan
    private function pickColor($seed)
    {
        $colors = [
            'bg-[#E0E7FF] text-[#4F46E5]',
            'bg-[#DCFCE7] text-[#16A34A]',
            'bg-[#FEF3C7] text-[#D97706]',
            'bg-[#FCE7F3] text-[#DB2777]',
            'bg-[#E0F2FE] text-[#0284C7]',
            'bg-[#EDE9FE] text-[#7C3AED]',
        ];

        $index = crc32((string) $seed) % count($colors);

        return $colors[$index];
    }
}