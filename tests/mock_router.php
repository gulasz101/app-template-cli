<?php

declare(strict_types=1);

header('Content-Type: '.getenv('API_CONTENT_TYPE'));

echo getenv('API_CONTENT_BODY');
