CREATE OR REPLACE FUNCTION group_recalc_active_group_members()
    RETURNS TRIGGER AS $$
BEGIN
    -- runs on group after saving, we need to to update user_group_active_members
    -- if the parent has changed from null to something, or something to other, then need to redo the user_group_active_members


    -- if going to null then
        --  delete any user_group_active_members.parent_user_group_id = OLD.
        --  insert any missing members of this group to the user_group_active_members

    -- if coming from null

        -- if union then add all the parent's members to user_group_active_members if they are missing , set parent for the new ones on that table
        -- if intersection then for any of our members not in the parent's members, remove those from user_group_active_members


    -- if going from something to other
        -- remove the something entries, if any, from this groups user_group_active_members where the two parent columns match
        -- do same as null above

    -- todo fill in proc and test
    RETURN NEW;
END;
$$ language 'plpgsql';
