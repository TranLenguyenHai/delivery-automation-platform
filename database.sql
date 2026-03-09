CREATE TABLE orders (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        customer_name VARCHAR(255),
                        customer_phone VARCHAR(20),
                        address VARCHAR(500),
                        packageWeight INT NOT NULL COMMENT 'Trọng lượng tính bằng gram',
                        status VARCHAR(50) DEFAULT 'PENDING',
                        shipper VARCHAR(50)
);

-- Thêm dòng này vào để thấy dữ liệu mẫu
INSERT INTO orders (customer_name, customer_phone, address, packageWeight, status)
VALUES ('Nguyen Van Thai', '0901234567', 'Da Nang, Vietnam', 600, 'PENDING');