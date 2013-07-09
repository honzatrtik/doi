<?php

$app->post('/doi', 'controller.doi:postDoi')->bind('doi_get');