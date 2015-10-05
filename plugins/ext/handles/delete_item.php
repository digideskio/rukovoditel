<?php

db_query("delete from app_ext_ganttchart_depends where item_id='" . db_input($_GET['id']) . "' or depends_id='" . db_input($_GET['id']) . "'");