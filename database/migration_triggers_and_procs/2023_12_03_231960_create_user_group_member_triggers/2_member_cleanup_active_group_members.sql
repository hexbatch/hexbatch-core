CREATE OR REPLACE FUNCTION member_cleanup_active_group_members()
    RETURNS TRIGGER AS $$
BEGIN
    -- runs on member before deleting, we may need to to update user_group_active_members
    -- if owner not a parent or child then return

    -- if owner is a parent , then there can be one or many child groups. Get the owner's parent combine strategy
    -- for each child group
        -- if union, see if child does has this user in their list, if so then set user_group_active_members.parent_user_group_id to null
        -- if intersection then if this user is also in the child list, add this to the user_group_active_members with parent there set to null

    -- todo fill in proc and test
    RETURN NEW;
END;
$$ language 'plpgsql';
