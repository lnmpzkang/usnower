create user usnower@'localhost' identified by 'usnower';
create database usnower default charset utf8;
grant all on usnower.* to usnower;


/*------------------------------------------------
USNOWER_ADMIN
------------------------------------------------*/
CREATE TABLE USNOWER_ADMIN (
        ID INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
        ADMIN VARCHAR(30) NOT NULL UNIQUE KEY,
        PWD VARCHAR(32) NOT NULL COMMENT 'MD5',
        IN_TIME TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),
        LAST_TIME TIMESTAMP,
        LAST_IP INT COMMENT 'INET_ATON'
);

DELIMITER //
CREATE TRIGGER USNOWER_ADMIN_I BEFORE INSERT ON USNOWER_ADMIN FOR EACH ROW
BEGIN
        SET NEW.PWD = MD5(NEW.PWD);
END//

////////////////////////////////////////////////////////////////////////////////////////////////////////
CREATE TRIGGER USNOWER_ADMIN_U BEFORE UPDATE ON USNOWER_ADMIN FOR EACH ROW
BEGIN
        IF(NEW.PWD != OLD.PWD) THEN
        SET NEW.PWD = MD5(NEW.PWD);
        END IF;
END//

CREATE FUNCTION USNOWER_F_ISADMIN(IN_ADMIN VARCHAR(30),IN_PWD VARCHAR(32)) RETURNS BOOL
BEGIN
        DECLARE N BOOL;
        SELECT COUNT(1) INTO N FROM USNOWER_ADMIN WHERE ADMIN = IN_ADMIN AND PWD = MD5(IN_PWD);
        RETURN N;
END//

CREATE PROCEDURE USNOWER_P_ADMIN_LOGIN(IN IN_ADMIN VARCHAR(30),IN IN_IP VARCHAR(15))
BEGIN
        UPDATE USNOWER_ADMIN SET LAST_TIME = CURRENT_TIMESTAMP(),LAST_IP = INET_ATON(IN_IP) WHERE ADMIN = IN_ADMIN;
END//
DELIMITER ;


/*-----------------------------------------------------
USNOWER_UTRACK
-----------------------------------------------------*/
CREATE TABLE USNOWER_UTRACK(
        ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        REQUIRE_TIME TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),        

        IP INT NOT NULL COMMENT 'INET_ATON',
        OS VARCHAR(10),
        LANG VARCHAR(10),
        BROWSER VARCHAR(10),
        BROWSER_VERSION VARCHAR(10),
        FLASH_VERSION VARCHAR(10),
        COOKIE_ENABLED BOOL,
        JAVA_ENABLED BOOL,
        
        URL VARCHAR(300) COMMENT 'location.href',
        REFERRER VARCHAR(300) COMMENT 'document.referrer',

        CLIENT_ID VARCHAR(30) COMMENT 'FROM COOKIE'
)


/*-------------------------------------------------------------
USNOWER_ART_CAT 文章分类


CREATE TABLE USNOWER_ART_TYPE (
        ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        NAME VARCHAR(30) NOT NULL,
        FA_ID INT NOT NULL DEFAULT -1 COMMENT 'FATHER ID,IF -1 THEN NO FATHER',
        IN_TIME TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),
        CONSTRAINT UNIQUE KEY USNOWER_UNI_ART_TYPE_NAME (FA_ID,NAME)
);

--------------------------------------------------------------*/
CREATE TABLE USNOWER_ART_CAT (
  ID int(11) NOT NULL AUTO_INCREMENT,
  NAME varchar(30) NOT NULL,
  FA_ID int NOT NULL DEFAULT 0 COMMENT 'FATHER ID,IF 0 THEN NO FATHER',
  IN_TIME timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  UNIQUE KEY (`FA_ID`,`NAME`)
);

CREATE OR REPLACE VIEW USNOWER_V_ART_CAT 
(ID,NAME,FA_ID,FA_NAME,SUB_NUM)
AS
SELECT
        A.ID,A.NAME,
        A.FA_ID,B.NAME AS FA_NAME,
        (SELECT COUNT(1) FROM USNOWER_ART_CAT WHERE FA_ID = A.ID) AS SUB_NUM
FROM
        USNOWER_ART_CAT A LEFT JOIN
        USNOWER_ART_CAT B ON A.FA_ID = B.ID;

DELIMITER //

CREATE FUNCTION USNOWER_F_ART_CAT_PATH(IN_ID INT) RETURNS VARCHAR(1000)
BEGIN
        DECLARE V_NAME_PATH VARCHAR(1000);
        DECLARE V_ID_PATH VARCHAR(1000);

        DECLARE V_ID INT DEFAULT IN_ID;
        DECLARE V_FAID INT;
        DECLARE V_FANAME VARCHAR(30);
        DECLARE V_B BOOL DEFAULT FALSE;
        
        LAB1:LOOP

                SELECT FA_ID,FA_NAME INTO V_FAID,V_FANAME FROM USNOWER_V_ART_CAT WHERE ID = V_ID;

                IF NOT ISNULL(V_FAID) && V_FAID <> 0 THEN
                        SET V_NAME_PATH = CONCAT_WS(',',V_FANAME,V_NAME_PATH);
                        SET V_ID_PATH = CONCAT_WS(',',V_FAID,V_ID_PATH);
                        SET V_ID = V_FAID;
                        SET V_B = TRUE;
                ELSE
                        LEAVE LAB1;
                END IF;

        END LOOP LAB1;
 
        IF V_B THEN
               RETURN CONCAT_WS('|',V_NAME_PATH,V_ID_PATH);
        ELSE
               RETURN '';
        END IF;
END//

/*
ERROR 1424 (HY000): Recursive stored functions and triggers are not allowed.

CREATE FUNCTION USNOWER_F_SUB_CAT(IN_ID INT) RETURNS VARCHAR(1000)
BEGIN
  DECLARE V_STOP BOOLEAN DEFAULT TRUE;
  DECLARE V_ID INT;
  DECLARE V_SUB_IDS VARCHAR(1000);
  DECLARE V_IDS VARCHAR(1000);

  DECLARE CUR1 CURSOR FOR SELECT ID FROM USNOWER_ART_CAT WHERE FA_ID = IN_ID;
  DECLARE EXIT HANDLER FOR SQLSTATE '02000' SET V_STOP = FALSE;
  OPEN CUR1;
  
  WHILE V_STOP DO
    FETCH CUR1 INTO V_ID;
    SELECT USNOWER_F_SUB_CAT(V_ID) INTO V_SUB_IDS;
    SET V_IDS = CONCAT_WS(',',V_ID,V_SUB_IDS);
  END WHILE;
  RETURN V_IDS;
END//
*/

/*ERROR 1456 (HY000): Recursive limit 0 (as set by the max_sp_recursion_depth variable) was exceeded for routine */

CREATE PROCEDURE USNOWER_P_ART_SUB_CAT(IN IN_ID INT,OUT OUT_IDS VARCHAR(1000))
BEGIN
  DECLARE V_STOP BOOLEAN DEFAULT FALSE;
  DECLARE V_ID INT;
  DECLARE V_SUB_IDS VARCHAR(1000);
  DECLARE V_IDS VARCHAR(1000);

  DECLARE CUR1 CURSOR FOR SELECT ID FROM USNOWER_ART_CAT WHERE FA_ID = IN_ID;
  DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET V_STOP = TRUE;

  SET @@max_sp_recursion_depth = 10;

  OPEN CUR1;
  
  LAB1:WHILE NOT V_STOP DO
    FETCH CUR1 INTO V_ID;
    IF V_STOP THEN
      LEAVE LAB1;
    END IF;
    CALL USNOWER_P_ART_SUB_CAT(V_ID,V_SUB_IDS);
    SET V_IDS = CONCAT_WS(',',V_IDS,V_ID,V_SUB_IDS);
  END WHILE LAB1;
  SET OUT_IDS = V_IDS;
END//


CREATE FUNCTION USNOWER_F_ART_SUB_CAT(IN_ID INT) RETURNS VARCHAR(1000)
BEGIN
  DECLARE V_SUB_IDS VARCHAR(1000);
  CALL USNOWER_P_ART_SUB_CAT(IN_ID,V_SUB_IDS);
  RETURN CONCAT_WS(',',IN_ID,V_SUB_IDS);
END//

/*
SELECT ID,TITLE,CATEGORY FROM USNOWER_ART WHERE FIND_IN_SET(CATEGORY,USNOWER_F_SUB_CAT(0));
*/

DELIMITER ;


/*-----------------------------------------------------
USNOWER_ALBUM
-----------------------------------------------------*/
CREATE TABLE USNOWER_ALBUM (
 ID INT(11) NOT NULL AUTO_INCREMENT,
 NAME VARCHAR(30) NOT NULL,subject
 DESCRIPTION VARCHAR(1000) DEFAULT NULL,
 IN_TIME TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY (`ID`),
 UNIQUE KEY  (`NAME`)
);

/*-----------------------------------------------------
USNOWER_ART
-----------------------------------------------------*/

CREATE TABLE USNOWER_ART(
  ID INT AUTO_INCREMENT NOT NULL,
  TITLE VARCHAR(300) NOT NULL,
  AUTHOR VARCHAR(30),
  COME_FROM VARCHAR(300),
  IN_TIME TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONTENT TEXT,
  
  TITLE_COLOR VARCHAR(7) COMMENT '标题的显示颜色',
  TITLE_B BOOL DEFAULT FALSE COMMENT '标题是否是加粗显示',
  TITLE_I BOOL DEFAULT FALSE COMMENT '标题是否斜体显示',
  TITLE_U BOOL DEFAULT FALSE COMMENT '标题是否加下划线',
  
  SHOW_ABLE BOOL DEFAULT TRUE COMMENT '文章是否可见，如果不可见，不会显示在列表中，也不会显示任何内容',
  COMMENT_ABLE BOOL DEFAULT TRUE COMMENT '文章是否接受评论',

  CATEGORY INT NOT NULL COMMENT '文章属于哪个分类',  

  PRIMARY KEY (`ID`),
  FOREIGN KEY (`CATEGORY`) REFERENCES USNOWER_ART_CAT(`ID`)
);

/*-----------------------------------------------------
USNOWER_ART_ALBUM
-----------------------------------------------------*/

CREATE TABLE USNOWER_ART_ALBUM(
  ID INT AUTO_INCREMENT NOT NULL,
  ART INT NOT NULL,
  ALBUM INT NOT NULL,

  PRIMARY KEY(`ID`),
  FOREIGN KEY (`ALBUM`) REFERENCES USNOWER_ALBUM(`ID`),
  FOREIGN KEY (`ART`) REFERENCES USNOWER_ART(`ID`),
  UNIQUE KWY (`ALBUM`,`ART`)
);

/*-----------------------------------------------------
USNOWER_KEYWORD
-----------------------------------------------------*/
CREATE TABLE USNOWER_KEYWORD (
  ID INT AUTO_INCREMENT NOT NULL,
  KEYWORD VARCHAR(30),

  PRIMARY KEY (`ID`),
  UNIQUE KEY (`KEYWORD`)
);

/*-----------------------------------------------------
USNOWER_ART_KEYWORD
-----------------------------------------------------*/
CREATE TABLE USNOWER_ART_KEYWORD (
  ID INT AUTO_INCREMENT NOT NULL,
  ART INT NOT NULL,  
  KEYWORD INT NOT NULL,  

  PRIMARY KEY (`ID`),
  FOREIGN KEY (`ART`) REFERENCES USNOWER_ART (`ID`),
  FOREIGN KEY (`KEYWORD`) REFERENCES USNOWER_KEYWORD (`ID`)
);

/*-----------------------------------------------------

-----------------------------------------------------*/

DELIMITER //
/*如果写成事OUT的存储过程，PHP需要查两次数据库，因为只需要送出一个结果，所以还是写成FUNCTION好了。*/

CREATE PROCEDURE USNOWER_P_RECORD_KEYWORD( IN IN_KEYWORD VARCHAR(30) ,OUT OUT_ID INT)
BEGIN
  DECLARE V_ID INT;
  DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET V_ID = NULL;/*如果不SET V_ID = NULL,就会报错,但是V_ID本来就是null*/
  SELECT ID INTO V_ID FROM USNOWER_KEYWORD WHERE LOWER(KEYWORD) = LOWER(IN_KEYWORD);
  IF ISNULL(V_ID) THEN
    INSERT INTO USNOWER_KEYWORD (KEYWORD) VALUES (IN_KEYWORD);
    SET V_ID = LAST_INSERT_ID();
  END IF;
  SET OUT_ID = V_ID;
END//

CREATE FUNCTION USNOWER_F_RECORD_KEYWORD( IN_KEYWORD VARCHAR(30) ) RETURNS INT
BEGIN
  DECLARE V_ID INT;
  DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET V_ID = NULL;
  SELECT ID INTO V_ID FROM USNOWER_KEYWORD WHERE LOWER(KEYWORD) = LOWER(IN_KEYWORD);
  IF ISNULL(V_ID) THEN
    INSERT INTO USNOWER_KEYWORD (KEYWORD) VALUES (IN_KEYWORD);
    SET V_ID = LAST_INSERT_ID();
  END IF;
  RETURN V_ID;
END//

DELIMITER ;

/*-----------------------------------------------------

-----------------------------------------------------*/

DELIMITER //
CREATE FUNCTION USNOWER_F_ART_ALBUMS(IN_ART INT) RETURNS VARCHAR(1000)
BEGIN
  DECLARE V_ALBUM INT;
  DECLARE V_ALBUM_NAME VARCHAR(30);
  DECLARE V_ALBUMS VARCHAR(1000);
  DECLARE V_ALBUM_NAMES VARCHAR(1000);
  DECLARE V_STOP BOOL DEFAULT FALSE;
  DECLARE CUR_1 CURSOR FOR SELECT A.ALBUM,B.NAME AS ALBUM_NAME FROM USNOWER_ART_ALBUM A LEFT JOIN USNOWER_ALBUM B ON A.ALBUM = B.ID WHERE A.ART = IN_ART;
  DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET V_STOP = TRUE;
  OPEN CUR_1;

  REPEAT
    FETCH CUR_1 INTO V_ALBUM,V_ALBUM_NAME;
    IF NOT V_STOP THEN
      SET V_ALBUMS = CONCAT_WS(',',V_ALBUMS,V_ALBUM);
      SET V_ALBUM_NAMES = CONCAT_WS(',',V_ALBUM_NAMES,V_ALBUM_NAME);
    END IF;
  UNTIL V_STOP END REPEAT;

  CLOSE CUR_1;
  RETURN CONCAT_WS('|',V_ALBUM_NAMES,V_ALBUMS);
END//


CREATE FUNCTION USNOWER_F_ART_KEYWORDS(IN_ART INT) RETURNS VARCHAR(1000)
BEGIN
  DECLARE V_KEYWORD INT;
  DECLARE V_KEYWORD_NAME VARCHAR(30);
  DECLARE V_KEYWORDS VARCHAR(1000);
  DECLARE V_KEYWORD_NAMES VARCHAR(1000);
  DECLARE V_STOP BOOL DEFAULT FALSE;
  DECLARE CUR_1 CURSOR FOR SELECT B.ID,B.KEYWORD FROM USNOWER_ART_KEYWORD A LEFT JOIN USNOWER_KEYWORD B ON A.KEYWORD = B.ID WHERE A.ART = IN_ART;
  DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET V_STOP = TRUE;

  OPEN CUR_1;
  REPEAT
    FETCH CUR_1 INTO V_KEYWORD,V_KEYWORD_NAME;
    IF NOT V_STOP THEN
      SET V_KEYWORDS = CONCAT_WS(',',V_KEYWORDS,V_KEYWORD);
      SET V_KEYWORD_NAMES = CONCAT_WS(',',V_KEYWORD_NAMES,V_KEYWORD_NAME);
    END IF;
  UNTIL V_STOP END REPEAT;
  CLOSE CUR_1;
  RETURN CONCAT_WS('|',V_KEYWORD_NAMES,V_KEYWORDS);
END//

/*
CREATE PROCEDURE USNOWER_P_UPDAET_ART_KEYWORDS(IN IN_ART INT,IN IN_KEYWORDS VARCHAR(1000))
BEGIN

  DELETE FROM USNOWER_ART_KEYWORD WHERE ID IN (
    SELECT C.ID FROM(
          SELECT
          A.ID
          FROM
          USNOWER_ART_KEYWORD A LEFT JOIN
          USNOWER_KEYWORD B  ON A.KEYWORD = B.ID
          WHERE
          A.ART = IN_ART AND
          B.KEYWORD NOT IN (IN_KEYWORDS)
    ) C
  );

END//
*/

DELIMITER ;

/*-----------------------------------------------------

-----------------------------------------------------*/
CREATE OR REPLACE VIEW USNOWER_V_ART
(
ID,TITLE,AUTHOR,COME_FROM,IN_TIME,TITLE_COLOR,TITLE_B,TITLE_I,TITLE_U,SHOW_ABLE,COMMENT_ABLE,
CAT_ID,CAT_NAME,CAT_PATH,
ALBUMS,KEYWORDS
) AS
SELECT
  A.ID,A.TITLE,A.AUTHOR,A.COME_FROM,A.IN_TIME,
  A.TITLE_COLOR,A.TITLE_B,A.TITLE_I,A.TITLE_U,
  A.SHOW_ABLE,A.COMMENT_ABLE,A.CATEGORY AS CAT_ID,
  B.NAME AS CAT_NAME,USNOWER_F_ART_CAT_PATH(B.ID) AS CAT_PATH,
  USNOWER_F_ART_ALBUMS(A.ID) AS ALBUMS,
  USNOWER_F_ART_KEYWORDS(A.ID) AS KEYWORDS
FROM
  USNOWER_ART A LEFT JOIN
  USNOWER_ART_CAT B ON A.CATEGORY = B.ID;


/*-----------------------------------------------------
USNOWER_BAG_CAT
-----------------------------------------------------*/
CREATE TABLE USNOWER_BAG_CAT (
  ID int(11) NOT NULL AUTO_INCREMENT,
  NAME varchar(30) NOT NULL,
  FA_ID int NOT NULL DEFAULT 0 COMMENT 'FATHER ID,IF 0 THEN NO FATHER',
  IN_TIME timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  UNIQUE KEY (`FA_ID`,`NAME`)
);


CREATE OR REPLACE VIEW USNOWER_V_BAG_CAT 
(ID,NAME,FA_ID,FA_NAME,SUB_NUM)
AS
SELECT
        A.ID,A.NAME,
        A.FA_ID,B.NAME AS FA_NAME,
        (SELECT COUNT(1) FROM USNOWER_BAG_CAT WHERE FA_ID = A.ID) AS SUB_NUM
FROM
        USNOWER_BAG_CAT A LEFT JOIN
        USNOWER_BAG_CAT B ON A.FA_ID = B.ID;

DELIMITER //


CREATE FUNCTION USNOWER_F_BAG_CAT_PATH(IN_ID INT) RETURNS VARCHAR(1000)
BEGIN
        DECLARE V_NAME_PATH VARCHAR(1000);
        DECLARE V_ID_PATH VARCHAR(1000);

        DECLARE V_ID INT DEFAULT IN_ID;
        DECLARE V_FAID INT;
        DECLARE V_FANAME VARCHAR(30);
        DECLARE V_B BOOL DEFAULT FALSE;
        
        LAB1:LOOP

                SELECT FA_ID,FA_NAME INTO V_FAID,V_FANAME FROM USNOWER_V_BAG_CAT WHERE ID = V_ID;

                IF NOT ISNULL(V_FAID) && V_FAID <> 0 THEN
                        SET V_NAME_PATH = CONCAT_WS(',',V_FANAME,V_NAME_PATH);
                        SET V_ID_PATH = CONCAT_WS(',',V_FAID,V_ID_PATH);
                        SET V_ID = V_FAID;
                        SET V_B = TRUE;
                ELSE
                        LEAVE LAB1;
                END IF;

        END LOOP LAB1;
 
        IF V_B THEN
               RETURN CONCAT_WS('|',V_NAME_PATH,V_ID_PATH);
        ELSE
               RETURN '';
        END IF;
END//

CREATE PROCEDURE USNOWER_P_BAG_SUB_CAT(IN IN_ID INT,OUT OUT_IDS VARCHAR(1000))
BEGIN
  DECLARE V_STOP BOOLEAN DEFAULT FALSE;
  DECLARE V_ID INT;
  DECLARE V_SUB_IDS VARCHAR(1000);
  DECLARE V_IDS VARCHAR(1000);

  DECLARE CUR1 CURSOR FOR SELECT ID FROM USNOWER_BAG_CAT WHERE FA_ID = IN_ID;
  DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET V_STOP = TRUE;

  SET @@max_sp_recursion_depth = 10;

  OPEN CUR1;
  
  LAB1:WHILE NOT V_STOP DO
    FETCH CUR1 INTO V_ID;
    IF V_STOP THEN
      LEAVE LAB1;
    END IF;
    CALL USNOWER_P_BAG_SUB_CAT(V_ID,V_SUB_IDS);
    SET V_IDS = CONCAT_WS(',',V_IDS,V_ID,V_SUB_IDS);
  END WHILE LAB1;
  SET OUT_IDS = V_IDS;
END//


CREATE FUNCTION USNOWER_F_BAG_SUB_CAT(IN_ID INT) RETURNS VARCHAR(1000)
BEGIN
  DECLARE V_SUB_IDS VARCHAR(1000);
  CALL USNOWER_P_BAG_SUB_CAT(IN_ID,V_SUB_IDS);
  RETURN CONCAT_WS(',',IN_ID,V_SUB_IDS);
END//

DELIMITER ;

/*------------------------------------------------------
USNOWER_BAG
------------------------------------------------------*/

CREATE TABLE USNOWER_BAG (
  ID INT NOT NULL AUTO_INCREMENT,
  NAME VARCHAR(30) NOT NULL COMMENT '名称',
  NO VARCHAR(30) NOT NULL COMMENT '款号',
  SIZE_L INT COMMENT '长',
  SIZE_W INT COMMENT '宽',
  SIZE_H INT COMMENT '高',
  UNIT VARCHAR(30) COMMENT '单位',
  FABRIC VARCHAR(300) COMMENT '主要成份',
  DESCRIPTION TEXT COMMENT '描述',
  CAT INT NOT NULL,
  IN_TIME TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),
  
  PRIMARY KEY (`ID`),
  UNIQUE KEY (`NO`),
  FOREIGN KEY (`CAT`) REFERENCES USNOWER_BAG_CAT(`ID`)
);

CREATE TABLE USNOWER_BAG_PIC(
  ID INT NOT NULL AUTO_INCREMENT,
  BAG INT NOT NULL,
  COLOR VARCHAR(30),
  FILE VARCHAR(300) NOT NULL,
  IN_TIME TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),

  PRIMARY KEY (`ID`),
  FOREIGN KEY (`BAG`) REFERENCES USNOWER_BAG(`ID`),
  UNIQUE KEY (`FILE`)
);

/*-----------------------------------------------------
以下无用，为测试程序
分页，存储过程版
-----------------------------------------------------*/
DELIMITER //

CREATE PROCEDURE P_PAGINATION(OUT OUT_TOTAL_RECORD INT,OUT_TOTAL_PAGE INT, IN IN_SQL VARCHAR(3000),INOUT INOUT_CURRENTPAGE INT ,INOUT INOUT_PAGESIZE INT)
BEGIN
  SET @V_SQL = IN_SQL;

  PREPARE STMT FROM 'SELECT COUNT(1) INTO OUT_TOTAL_RECORD FROM (?)';
  EXECUTE STMT USING @V_SQL;

  IF INOUT_PAGESIZE <= 0 THEN
    SET INOUT_PAGESIZE = 20 ;
  END IF;
  IF INOUT_CURRENTPAGE < 0 THEN
    SET INOUT_CURRENTPAGE = 0 ;
  END IF;

  SET OUT_TOTAL_PAGE = CEIL( OUT_TOTAL_RECORD / INOUT_PAGESIZE);
  IF INOUT_CURRENTPAGE > OUT_TOTAL_PAGE THEN 
    SET INOUT_CURRENTPAGE = OUT_TOTAL_PAGE;
  END IF;

  SET @V_START = INOUT_CURRENTPAGE * INOUT_PAGESIZE;

  PREPARE STMT FROM 'SELECT * FROM (?) LIMIT ?,?';
  SET @V_PAGESIZE = INOUT_PAGESIZE;
  EXECUTE STMT USING @V_SQL,@V_START,@V_PAGESIZE;
END//




CREATE PROCEDURE TEST (IN IN_VAR INT,OUT OUT_VAR INT)
BEGIN
  SET @OUT_VAR = OUT_VAR;
  PREPARE STMT FROM 'SELECT ? FROM DUAL INTO @OUT_VAR';
  SET @IN_VAR = IN_VAR;
  EXECUTE STMT USING @IN_VAR;
END//

DELIMITER ;

SELECT 1 INTO @B FROM DUAL//
SELECT CONCAT_WS(',','1','2') FROM DUAL INTO @B//
SELECT @A:=CONCAT_WS(',','A','B') FROM DUAL;






/*
INSERT INTO table (a,b,c) VALUES (1,2,3)
ON DUPLICATE KEY UPDATE c=c+1;
*/

