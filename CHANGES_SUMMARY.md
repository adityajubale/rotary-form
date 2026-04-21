# Form Updates Summary

## Changes Made

### 1. ✅ Payment Section Moved to Bottom
- **Before**: QR Code, UTI Number, and Screenshot were at the top of the form
- **After**: Now positioned at the bottom, just before the Submit button
- **Forms Updated**: Single Registration, Couple Registration, Co-Hosting Platinum, Co-Hosting Gold

### 2. ✅ New Role Dropdown Added
- **Position**: Above the Designation field
- **Options**: 
  - COG
  - DISTRICT OFFICER
  - MEMBER
- **Forms Updated**: Single Registration and Couple Registration forms
- **Note**: Co-Hosting forms don't have this field (they don't use club/member selection)

### 3. ✅ UTR and Screenshot Made Mandatory
- **UTI Number/Transaction ID**: Required field (minimum 4 characters)
- **Payment Screenshot**: Required file upload
- **Validation**: Both fields must be filled before submission
- **Error Message**: User will see clear error if either field is missing

### 4. ✅ Database Schema Updated
Updated **rotary.sql** with new columns:
- `full_name` (varchar 200) - Added to both tables
- `role` (varchar 50) - Added to both tables with default value 'MEMBER'

### 5. ✅ PHP Backend Updated
Updated **save_registration.php**:
- Extract `role` field from form data
- Validate role is provided
- Save role to database in INSERT queries
- Updated both single and couple registration handlers

### 6. 📄 Migration Script Created
File: **migration_add_role_field.sql**
- Run this if you already have existing data in your database
- Adds missing columns without losing data

## Database Tables Updated
- `single_registrations`: Added `full_name` and `role` columns
- `couple_registrations`: Added `full_name` and `role` columns

## Form Field Order (Single & Couple)
1. Club Selection
2. Member Selection
3. **Role (NEW)** ← Dropdown above Designation
4. Designation
5. Mobile Number
6. Email
7. Food Preference
8. Alcohol Preference
9. (HR line separator)
10. **QR Code Section (MOVED TO BOTTOM)**
11. **UTI Number (MANDATORY)**
12. **Payment Screenshot (MANDATORY)**
13. Submit Button

## Testing Checklist
- [ ] Test single registration form with new role dropdown
- [ ] Test couple registration form with new role dropdown
- [ ] Verify payment section moved to bottom of form
- [ ] Try submitting without UTI number - should show error
- [ ] Try submitting without screenshot - should show error
- [ ] Try selecting a role and completing full submission
- [ ] Verify data is saved in database with role and full_name
- [ ] Check uploaded screenshots in /uploads folder
- [ ] Review error_log for any PHP errors

## Next Steps (if needed)
1. Back up your database
2. Run the migration script: **migration_add_role_field.sql**
3. Test the form in your browser
4. Check that data is saving correctly with new fields
