SELECT test_item_master.item_name, test_item_master.price, test_item_stock.stock FROM test_item_master JOIN test_item_stock ON test_item_master.id = test_item_stock.item_id