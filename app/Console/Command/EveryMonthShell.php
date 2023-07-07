<?php

class EveryMonthShell extends AppShell
{
    public $uses = array('Invoice');

    public function main()
    {
        $this->Invoice->billCron();
    }
}
