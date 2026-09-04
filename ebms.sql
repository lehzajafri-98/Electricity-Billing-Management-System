use EBMS;

-- ===============================
-- CLEANUP: DROP OBJECTS IF EXIST
-- ===============================

IF OBJECT_ID('trg_AfterInsert_Bill', 'TR') IS NOT NULL
    DROP TRIGGER trg_AfterInsert_Bill;
GO

IF OBJECT_ID('sp_GetCustomerBills', 'P') IS NOT NULL
    DROP PROCEDURE sp_GetCustomerBills;
GO

IF OBJECT_ID('sp_UpdateBillPaymentDate', 'P') IS NOT NULL
    DROP PROCEDURE sp_UpdateBillPaymentDate;
GO

IF OBJECT_ID('vw_BillingDetails', 'V') IS NOT NULL
    DROP VIEW vw_BillingDetails;
GO

DROP TABLE IF EXISTS Complaint;
DROP TABLE IF EXISTS TariffPlan;
DROP TABLE IF EXISTS Customer_ElecBoard;
DROP TABLE IF EXISTS Bill;
DROP TABLE IF EXISTS Elec_Board;
DROP TABLE IF EXISTS Customer;
DROP TABLE IF EXISTS Admin;
GO

-- ===============================
-- CREATE TABLES
-- ===============================

CREATE TABLE Customer (
    CustomerID INT IDENTITY PRIMARY KEY,
    Name VARCHAR(100),
    Address VARCHAR(200),
    Phone VARCHAR(15)
);

CREATE TABLE Elec_Board (
    BoardID INT IDENTITY PRIMARY KEY,
    BoardName VARCHAR(100),
    Region VARCHAR(100)
);

CREATE TABLE TariffPlan (
    PlanID INT IDENTITY PRIMARY KEY,
    BoardID INT FOREIGN KEY REFERENCES Elec_Board(BoardID),
    RatePerUnit DECIMAL(10, 2)
);

CREATE TABLE Customer_ElecBoard (
    CustomerID INT,
    BoardID INT,
    PRIMARY KEY (CustomerID, BoardID),
    FOREIGN KEY (CustomerID) REFERENCES Customer(CustomerID),
    FOREIGN KEY (BoardID) REFERENCES Elec_Board(BoardID)
);

CREATE TABLE Bill (
    BillID INT IDENTITY PRIMARY KEY,
    CustomerID INT FOREIGN KEY REFERENCES Customer(CustomerID),
    BillDate DATE,
    UnitsConsumed INT,
    RatePerUnit DECIMAL(10,2),
    BillAmount DECIMAL(12,2),
    PaymentDate DATE NULL
);

CREATE TABLE Complaint (
    ComplaintID INT IDENTITY PRIMARY KEY,
    CustomerID INT FOREIGN KEY REFERENCES Customer(CustomerID),
    Subject VARCHAR(100),
    Description TEXT,
    Status VARCHAR(50),
    CreatedAt DATETIME DEFAULT GETDATE()
);

CREATE TABLE Admin (
    AdminID INT PRIMARY KEY IDENTITY,
    Username VARCHAR(50) UNIQUE NOT NULL,
    Password VARCHAR(255) NOT NULL
);
GO

-- ===============================
-- INSERT DATA INTO Customer
-- ===============================

INSERT INTO Customer (Name, Address, Phone) VALUES
('Ali Khan', 'House 123, Gulshan-e-Iqbal, Karachi', '0300-1234567'),
('Fatima Zehra', 'Street 45, F-7, Islamabad', '0321-7654321'),
('Mehdi Hassan', 'Block B, Johar Town, Lahore', '0333-9876543'),
('Amed Malik', 'Sector G-10, Islamabad', '0345-2468101'),
('Zain Abbas', 'Street 12, Saddar, Karachi', '0312-5555555'),
('Sana Tariq', 'Flat 5B, PECHS, Karachi', '0301-9988776'),
('Rizwan Ali', 'Plot 22, Model Town, Lahore', '0322-7766554'),
('Hira Naveed', 'House 18, G-11/3, Islamabad', '0313-8844221'),
('Usman Sheikh', 'Main Road, Hyderabad', '0340-1122334'),
('Mehwish Rana', 'Street 8, Gulberg, Lahore', '0355-6677889');

SELECT * FROM Customer;
GO

-- ===============================
-- INSERT DATA INTO Elec_Board
-- ===============================

INSERT INTO Elec_Board (BoardName, Region) VALUES
('K-Electric', 'Karachi'),
('Lahore Electric Supply Company', 'Lahore'),
('Islamabad Electric Supply Company', 'Islamabad'),
('Sukkur Electric Power Company', 'Sukkur');

SELECT * FROM Elec_Board;
GO

-- ===============================
-- INSERT DATA INTO TariffPlan
-- ===============================

INSERT INTO TariffPlan (BoardID, RatePerUnit) VALUES
(1, 12.00),
(2, 11.50),
(3, 13.20),
(4, 10.75);

SELECT * FROM TariffPlan;
GO

-- ===============================
-- INSERT DATA INTO Customer_ElecBoard
-- ===============================

INSERT INTO Customer_ElecBoard (CustomerID, BoardID) VALUES
(1, 1),
(2, 3),
(3, 2),
(4, 3),
(5, 1),
(6, 1),
(7, 2),
(8, 3),
(9, 4),
(10, 2);

SELECT * FROM Customer_ElecBoard;
GO

-- ===============================
-- INSERT DATA INTO Bill
-- ===============================

INSERT INTO Bill (CustomerID, BillDate, UnitsConsumed, RatePerUnit, BillAmount, PaymentDate) VALUES
(1, '2025-01-01', 650, 12.00, 650 * 12.00, NULL),
(2, '2025-01-02', 200, 11.50, 200 * 11.50, '2025-01-10'),
(3, '2025-01-03', 380, 13.20, 380 * 13.20, NULL),
(4, '2025-01-04', 220, 10.00, 220 * 10.00, '2025-01-15'),
(5, '2025-01-05', 900, 15.00, 900 * 15.00, NULL),
(6, '2025-01-06', 330, 12.50, 330 * 12.50, '2025-01-12'),
(7, '2025-01-07', 290, 10.80, 290 * 10.80, NULL),
(8, '2025-01-08', 760, 11.25, 760 * 11.25, NULL),
(9, '2025-01-09', 740, 13.00, 740 * 13.00, '2025-01-18'),
(10,'2025-01-10', 875, 14.00, 875 * 14.00, NULL);

SELECT * FROM Bill;
GO

-- ===============================
-- INSERT DATA INTO Complaint
-- ===============================

INSERT INTO Complaint (CustomerID, Subject, Description, Status) VALUES
(1, 'Billing Error', 'Incorrect units charged.', 'Open'),
(3, 'No Power', 'Power outage for 3 days.', 'Resolved'),
(5, 'Meter Faulty', 'Meter is not working.', 'Pending');

SELECT * FROM Complaint;
GO

-- ===============================
-- INSERT INTO Admin
-- ===============================

INSERT INTO Admin (Username, Password) VALUES ('admin', 'admin123');
SELECT * FROM Admin;
GO

-- ===============================
-- CREATE TRIGGER: Auto update BillAmount
-- ===============================

CREATE TRIGGER trg_AfterInsert_Bill
ON Bill
AFTER INSERT, UPDATE
AS
BEGIN
    SET NOCOUNT ON;--Prevents the message that shows the count of affected rows from being returned to the client. 
    UPDATE b
    SET BillAmount = i.UnitsConsumed * i.RatePerUnit
    FROM Bill b
    INNER JOIN inserted i ON b.BillID = i.BillID;
END;
GO

-- ===============================
-- TEST TRIGGER
-- ===============================

UPDATE Bill SET UnitsConsumed = 160 WHERE BillID = 1;
SELECT * FROM Bill WHERE BillID = 1;
GO
-- ===============================
-- CREATE VIEW
-- ===============================

CREATE VIEW vw_BillingDetails AS
SELECT
    b.BillID,
    c.Name AS CustomerName,
    eb.BoardName,
    b.UnitsConsumed,
    b.RatePerUnit,
    b.BillAmount,
    b.BillDate,
    b.PaymentDate
FROM Bill b
JOIN Customer c ON b.CustomerID = c.CustomerID
JOIN Customer_ElecBoard ceb ON c.CustomerID = ceb.CustomerID
JOIN Elec_Board eb ON ceb.BoardID = eb.BoardID;
GO

SELECT * FROM vw_BillingDetails;
GO

-- ===============================
-- STORED PROCEDURE 1
-- ===============================

CREATE PROCEDURE sp_GetCustomerBills
    @CustomerName VARCHAR(100)
AS
BEGIN
    SET NOCOUNT ON;
    SELECT
        b.BillID,
        b.BillDate,
        b.UnitsConsumed,
        b.RatePerUnit,
        b.BillAmount,
        b.PaymentDate
    FROM Bill b
    JOIN Customer c ON b.CustomerID = c.CustomerID
    WHERE c.Name = @CustomerName;
END;
GO

EXEC sp_GetCustomerBills @CustomerName = 'Ali Khan';
GO

-- ===============================
-- STORED PROCEDURE 2
-- ===============================

CREATE PROCEDURE sp_UpdateBillPaymentDate
    @BillID INT,
    @PaymentDate DATE
AS
BEGIN
    SET NOCOUNT ON;
    UPDATE Bill
    SET PaymentDate = @PaymentDate
    WHERE BillID = @BillID;

    SELECT * FROM Bill WHERE BillID = @BillID;
END;
GO

EXEC sp_UpdateBillPaymentDate @BillID = 3, @PaymentDate = '2025-01-20';
GO

-- ===============================
--  Billed Customers in January 2025
-- ===============================

SELECT 
    c.Name AS CustomerName,
    b.BillDate,
    b.BillAmount
FROM Bill b
JOIN Customer c ON b.CustomerID = c.CustomerID
WHERE MONTH(b.BillDate) = 1 AND YEAR(b.BillDate) = 2025;
GO

-- ===============================
--  Total Units Consumed per Board
-- ===============================

SELECT 
    eb.BoardName,
    SUM(b.UnitsConsumed) AS TotalUnits
FROM Bill b
JOIN Customer c ON b.CustomerID = c.CustomerID
JOIN Customer_ElecBoard ceb ON c.CustomerID = ceb.CustomerID
JOIN Elec_Board eb ON ceb.BoardID = eb.BoardID
GROUP BY eb.BoardName;
GO

-- ===============================
-- tariffplan
-- ===============================

SELECT tp.PlanID, eb.BoardName, tp.RatePerUnit
FROM TariffPlan tp
LEFT JOIN Elec_Board eb ON tp.BoardID = eb.BoardID;
GO

-- ===============================
--  Customers with Bill > 2000
-- ===============================

SELECT 
    c.Name,
    b.BillAmount
FROM Bill b
JOIN Customer c ON b.CustomerID = c.CustomerID
WHERE b.BillAmount > 2000;
GO

-- ===============================
-- QUERY 9: Total Revenue Collected
-- ===============================

SELECT SUM(BillAmount) AS TotalCollectedRevenue
FROM Bill
WHERE PaymentDate IS NOT NULL;
GO

-- ===============================
-- BONUS: Average Units Consumed per Region
-- ===============================

SELECT 
    eb.Region,
    AVG(b.UnitsConsumed) AS AvgUnits
FROM Bill b
JOIN Customer c ON b.CustomerID = c.CustomerID
JOIN Customer_ElecBoard ceb ON c.CustomerID = ceb.CustomerID
JOIN Elec_Board eb ON ceb.BoardID = eb.BoardID
GROUP BY eb.Region;
GO


