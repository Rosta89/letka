<?php
Db::beginTransaction();
Db::query("INSERT INTO training (x,y) VALUES (?,?)", 3, 2);
Db::query("INSERT INTO training (x,y) VALUES (?,?,?)", 5, 2);
Db::commitTransaction();
