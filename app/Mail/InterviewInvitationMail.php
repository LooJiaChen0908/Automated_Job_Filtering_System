<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\ZoomMeeting;
use Carbon\Carbon;

class InterviewInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $plainText;
    public ZoomMeeting $meeting;

    /**
     * Create a new message instance.
     */
    public function __construct(ZoomMeeting $meeting)
    {
        $this->meeting = $meeting;

        $this->plainText =
            "Dear " . $meeting->application->applicant->name . ",\n\n" .
            "Your interview has been scheduled.\n\n" .
            "📌 Topic: " . $meeting->topic . "\n" .
            "📅 Date & Time: " . Carbon::parse($meeting->start_time)->format('l, d F Y h:i A') . " (" . $meeting->timezone . ")\n" .
            "🔗 Join URL: " . $meeting->join_url . "\n\n" .
            "Please make sure you are available at the scheduled time. If you have any issues joining, contact our recruitment team.\n\n" .
            "Best regards,\nYour Recruitment Team";
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Interview Zoom Meeting Details',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            text: 'emails.interview_invitation_plain',
            with: [
                'plainText' => $this->plainText,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}