<?php

namespace CVAdmin\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Factura extends Mailable
{
    use Queueable, SerializesModels;

    public $vars;
    public $subject;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $v)
    {
        //
        $this->subject = $subject;
        $this->vars = $v;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $build = $this->from('noreply@centrosvirtuales.com')
                    ->subject($this->subject)
                    ->view("mail.html.factura", $this->vars);

        if(isset($this->vars['attach']))
            $build->attachData($this->vars['attach'][0],$this->vars['attach'][1],$this->vars['attach'][2]);

        if(isset($this->vars['cc'])){
            $build->cc($this->vars['cc']);
        }

        return $build;
    }
}
