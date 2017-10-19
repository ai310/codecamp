SELECT
    id, name, price
FROM
    products
WHERE
    name = 'マウスパッド';
    name LIKE '%パッド%';
    name NOT LIKE '%パッド%' AND price >= 500;