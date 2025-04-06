namespace Tissue;

model Metadata {
  url: url;
  title: string;
  description: string;
  image: url;
  expires_at: string | null;
  tags: string[];
}
