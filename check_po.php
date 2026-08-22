<?php
print_r(DB::select("SELECT column_name, data_type FROM information_schema.columns WHERE table_name = 'sale_return_items' OR table_name = 'sale_returns' ORDER BY table_name, ordinal_position"));
