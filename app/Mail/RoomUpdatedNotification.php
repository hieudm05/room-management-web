<?php

namespace App\Mail;

use App\Models\Landlord\Room;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RoomUpdatedNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Room $room;
    public array $changes;

    /**
     * Create a new message instance.
     */
    public function __construct(Room $room, array $changes)
    {
        $this->room = $room;
        $this->changes = $changes;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Phòng ' . $this->room->room_number . ' đã được cập nhật',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.room_updated',
            with: [
                'room' => $this->room,
                'changes' => $this->changes,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
