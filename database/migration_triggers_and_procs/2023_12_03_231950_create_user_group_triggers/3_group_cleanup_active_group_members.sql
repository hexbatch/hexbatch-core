CREATE OR REPLACE FUNCTION group_undo_active_group_members()
    RETURNS TRIGGER AS $$
BEGIN
    -- runs on group before deleting, we may need to to update user_group_active_members
    -- if not a parent then return

    -- always set user_group_active_members.parent_user_group_id to null for this group id
    -- if intersection then for any of the child group members not on the user_group_active_members, add them with null parent id

    -- todo fill in proc and test
    RETURN NEW;
END;
$$ language 'plpgsql';
