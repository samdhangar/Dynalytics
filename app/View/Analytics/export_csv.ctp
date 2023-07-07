<?php
$data = array('Sr. no.', 'Branch Name', 'Station', 'Date');
$this->CSV->addRow(array_values($data));
$filename = 'posts';
echo $this->CSV->render($filename);

?>