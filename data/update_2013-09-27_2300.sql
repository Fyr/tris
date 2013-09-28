ALTER TABLE ls_media ADD `object_type` varchar(20) NOT NULL DEFAULT 'Lesson' AFTER `media_type`;
UPDATE ls_media SET object_type = 'Paragraph' WHERE media_type = 'audio';
