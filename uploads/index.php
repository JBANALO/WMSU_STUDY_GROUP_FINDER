<?php
// Prevent direct access to uploads directory
http_response_code(403);
die('Access denied');
