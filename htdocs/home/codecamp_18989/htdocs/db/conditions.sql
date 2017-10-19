SELECT
    id, name, price
FROM
    products
WHERE
    price BETWEEN 500 AND 1000
    OR id >= 3 AND price >= 500
    OR price <= 1500 AND name != '傘'