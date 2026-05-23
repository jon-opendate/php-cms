CREATE TABLE IF NOT EXISTS posts (
    id SERIAL PRIMARY KEY,
    title VARCHAR(180) NOT NULL,
    slug VARCHAR(180) NOT NULL UNIQUE,
    body TEXT NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'draft',
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_posts_status ON posts (status);

INSERT INTO posts (title, slug, body, status)
VALUES (
    'Welcome to your PHP CMS',
    'welcome',
    'This starter CMS is running on PHP with PostgreSQL. Edit or delete this post, then add your own.',
    'published'
)
ON CONFLICT (slug) DO NOTHING;
