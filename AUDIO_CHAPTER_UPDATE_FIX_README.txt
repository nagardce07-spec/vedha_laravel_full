Chapter audio replacement fix (Railway):
- Set Railway Settings > Deploy > Custom Start Command to the command in DEPLOY_README.md. It explicitly sets PHP upload_max_filesize=600M and post_max_size=620M for the actual PHP server process.
- Chapter edit page now displays upload/validation errors instead of silently showing the old audio after a failed upload.
- This targets only the chapter audio replacement issue; no database migration is required.
- Live Railway upload has not been tested from this environment.
