SELECT 
    student_id,
    SUM(CASE
        WHEN subject_id = 102 THEN present
        ELSE 0
    END) AS '102',
    SUM(CASE
        WHEN subject_id = 105 THEN present
        ELSE 0
    END) AS '105',
    SUM(CASE
        WHEN subject_id = 106 THEN present
        ELSE 0
    END) AS '106',
    SUM(CASE
        WHEN subject_id = 107 THEN present
        ELSE 0
    END) AS '107'
FROM
    (SELECT 
        stu.student_id,
            t.subject_id,
            total.total_classes,
            COALESCE(ab.absent, 0) AS absent,
            (total.total_classes - COALESCE(ab.absent, 0)) AS present
    FROM
        Student stu -- Corrected table name 
    CROSS JOIN (SELECT DISTINCT
        subject_id
    FROM
        attendance_log) t
    LEFT JOIN (SELECT 
        subject_id, COUNT(*) AS total_classes
    FROM
        attendance_log
    GROUP BY subject_id) total ON total.subject_id = t.subject_id
    LEFT JOIN (SELECT 
        student_id, subject_id, COUNT(*) AS absent
    FROM
        absentees_log
    GROUP BY student_id , subject_id) ab ON stu.student_id = ab.student_id
        AND t.subject_id = ab.subject_id) AS main
GROUP BY student_id ;
