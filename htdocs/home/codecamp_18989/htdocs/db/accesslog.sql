SELECT
    CAst(access_datetime AS DATE) AS date,
    COUNT(access_datetime) AS pv,
    COUNT(DISTINCT user_id) AS uu
FROM
    access
GROUP BY DATE
    (access_datetime);
