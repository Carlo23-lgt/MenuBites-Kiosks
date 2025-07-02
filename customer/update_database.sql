-- Add table_number and status columns to orders table if they don't exist
ALTER TABLE orders 
ADD COLUMN IF NOT EXISTS table_number VARCHAR(10) NULL,
ADD COLUMN IF NOT EXISTS status VARCHAR(20) DEFAULT 'active'; 