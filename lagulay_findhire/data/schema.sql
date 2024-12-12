CREATE TABLE HumanResource (
    HRUser_id INT PRIMARY KEY AUTO_INCREMENT,
    password TEXT,
    name VARCHAR(100),
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
);

CREATE TABLE Applicant (
    APUser_id INT PRIMARY KEY AUTO_INCREMENT,
    password TEXT,
    name VARCHAR(100),
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
);

CREATE TABLE Post (
    HRUser_id INT ,
    Post_id INT ,
    Description VARCHAR(300),
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
);

CREATE TABLE Comments (
    Comment_id INT PRIMARY KEY AUTO_INCREMENT,
    APUser_id INT ,
    Post_id INT ,
    Description VARCHAR(300),
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
);

CREATE TABLE ApplicantLogs(
    AR_id INT ,
    content VARCHAR(300),
    HRUser_id INT ,
    APUser_id INT,
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
);
CREATE TABLE Messages(
    Message_id INT,
    APUser_id INT,
    HRUser_id INT,
    content VARCHAR(300),
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
);