<?php

namespace CVAdmin\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Correspondencia extends Mailable
{
    use Queueable, SerializesModels;

    public $vars;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($v)
    {
        $this->vars = $v;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('noreply@centrosvirtuales.com')
                    ->subject('NUEVA CORRESPONDENCIA')
                    ->view('mail.html.correspondencia')
                    ->with($this->vars);
    }
}
