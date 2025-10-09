<?php
$this->load->view($module . '/includes/header',['title' => $title]);
$this->load->view($module . '/' . $content);
$this->load->view($module . '/includes/footer');