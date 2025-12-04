# Quick Start Guide: CSV Import with Progress Tracking

## Prerequisites
- Laravel application running
- Database migrated
- At least one brand and email list created

## Step 1: Start Queue Worker

Open a terminal and run:
```bash
cd /home/leopathu/Public/inboxy
php artisan queue:work --tries=3 --backoff=60
```

**Keep this terminal open!** The queue worker needs to run continuously to process import jobs.

For testing only (processes one job then exits):
```bash
php artisan queue:work --once
```

## Step 2: Create Test CSV File

Create a file named `test-subscribers.csv`:

```csv
email,first_name,last_name
john.doe@example.com,John,Doe
jane.smith@example.com,Jane,Smith
bob.johnson@example.com,Bob,Johnson
alice.williams@example.com,Alice,Williams
charlie.brown@example.com,Charlie,Brown
```

Save this file to your desktop or downloads folder.

## Step 3: Import Subscribers

1. **Login** to your application
2. **Navigate** to a list details page:
   - Go to "Lists" in the navigation
   - Click on any list name
3. **Click** "Import CSV" button (green button at top)
4. **Select** your test CSV file
5. **Ensure** "Has header row" checkbox is checked
6. **Click** "Import Subscribers"

## Step 4: Watch Progress

You'll be redirected to the list details page where you'll see:

1. **Import Progress Card** appears at the top
2. **Status Badge**: 
   - Yellow "Pending" → Blue "Processing" → Green "Completed"
3. **Progress Bar**: Animates as rows are processed
4. **Statistics Update**: Every 3 seconds
   - Total Rows: 5
   - Processed: 0 → 1 → 2 → ... → 5
   - Imported: Count increases
   - Skipped: If duplicates found
   - Errors: If any validation issues

5. **Completion Message**: Shows when done
6. **Subscriber List**: Refreshes automatically

## Step 5: Verify Results

### Check Subscribers Table
Scroll down to see your newly imported subscribers in the table.

### Check Database
```bash
php artisan tinker
```

Then run:
```php
// Check import record
\App\Models\SubscriberImport::latest()->first();

// Check imported subscribers
\App\Models\Subscriber::latest()->take(5)->get();
```

## Troubleshooting

### Import Stuck at "Pending"
**Problem**: Import status doesn't change from "Pending"

**Solution**: Queue worker not running
```bash
# Terminal 1: Check if queue worker is running
ps aux | grep "queue:work"

# Terminal 2: Start queue worker
php artisan queue:work
```

### No Progress Updates
**Problem**: Progress bar doesn't move

**Solution**: Check browser console for errors
```javascript
// Open browser DevTools (F12)
// Check Console tab for errors
// Look for network errors to /imports/status
```

### Import Shows "Failed"
**Problem**: Import status changes to "Failed"

**Solution**: Check error message in import card and logs
```bash
# Check Laravel logs
tail -f storage/logs/laravel.log

# Check failed jobs
php artisan queue:failed
```

### CSV Format Issues
**Problem**: Rows skipped or errors

**Solution**: Verify CSV format
- First row must be: `email,first_name,last_name`
- Emails must be valid format
- No empty email fields
- Use UTF-8 encoding
- No special characters in delimiter

## Advanced Testing

### Test Large File
Create a larger CSV with 1000+ rows to see:
- Progressive loading
- Smooth progress bar animation
- List count updates
- Performance optimization

### Test Error Handling

Create a CSV with invalid data:
```csv
email,first_name,last_name
john.doe@example.com,John,Doe
invalid-email,Jane,Smith
@example.com,Bob,Johnson
duplicate@example.com,Alice,Williams
duplicate@example.com,Charlie,Brown
```

This will test:
- Invalid email format detection
- Duplicate detection
- Error counting
- Error message display

### Test Concurrent Imports

1. Open multiple browser tabs
2. Import different CSVs to same list
3. Watch both progress in real-time
4. Verify all imports complete successfully

## Queue Worker Tips

### Run in Background (Production)
```bash
# Using nohup
nohup php artisan queue:work --tries=3 > /dev/null 2>&1 &

# Or use Laravel Horizon (recommended for production)
composer require laravel/horizon
php artisan horizon
```

### Monitor Queue
```bash
# Check queue status
php artisan queue:monitor

# Check failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all
```

### Supervisor Configuration (Production)
Create `/etc/supervisor/conf.d/inboxy-worker.conf`:
```ini
[program:inboxy-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /home/leopathu/Public/inboxy/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasecs=3600
user=leopathu
numprocs=2
redirect_stderr=true
stdout_logfile=/home/leopathu/Public/inboxy/storage/logs/worker.log
stopwaitsecs=3600
```

Then:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start inboxy-worker:*
```

## What to Look For

### Success Indicators
✅ Import card appears immediately after upload
✅ Status changes from Pending → Processing → Completed
✅ Progress bar animates smoothly
✅ Statistics update every 3 seconds
✅ List subscriber count updates
✅ Subscriber table refreshes when complete
✅ Success message shows completion time
✅ No errors in browser console
✅ No errors in Laravel logs

### Common Issues
❌ Import stuck at "Pending" - Queue worker not running
❌ Progress not updating - JavaScript errors or network issues
❌ Import failed - Invalid CSV format or database errors
❌ Skipped rows - Duplicates, suppressed emails, or invalid emails
❌ Slow processing - Large file or insufficient server resources

## Next Steps

Once basic import is working:
1. Test with larger files (10,000+ rows)
2. Test custom fields import
3. Test suppression list functionality
4. Test double opt-in workflow
5. Set up production queue worker with Supervisor
6. Configure Laravel Horizon for monitoring

## Support

If you encounter issues:
1. Check `storage/logs/laravel.log`
2. Check browser console (F12 → Console tab)
3. Verify queue worker is running: `ps aux | grep queue:work`
4. Check database: `SELECT * FROM subscriber_imports ORDER BY created_at DESC`
5. Check jobs queue: `SELECT * FROM jobs`
