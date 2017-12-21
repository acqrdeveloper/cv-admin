<?php

namespace CVAdmin\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Information extends Mailable
{
    use Queueable, SerializesModels;

    public $fullname;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($f)
    {
        //
        $this->fullname = $f;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('noreply@centrosvirtuales.com')
                    ->subject('NUEVA DIRECCIÓN COMERCIAL EN SURCO')
                    ->attach( storage_path("app/info.pdf") )
                    ->view('mail.html.info')
                    ->with(['fullname'=>$this->fullname]);
    }
}
