CREATE OR REPLACE FUNCTION members_recalc_active_group_members()
    RETURNS TRIGGER AS $$
BEGIN
    -- updating or inserting group member after saving, we need to decide if this member is going to be added to user_group_active_members or remove another


    -- get the owning group, from   user_groups.parent_user_group_id, if this has neither parent or child, make sure this entry is in user_group_active_members

    -- if the owner group is a child, then get the owner's parent combine strategy
       -- if union, then make sure this user is in user_group_active_members and has the parent set correctly there
       -- if intersection,
            -- if user is not a member of the owner group's members, make sure this does not exist or is deleted
            -- if user is also member, then make sure this user is in user_group_active_members and has the parent set correctly there

   -- if the owner group is a parent, then there can be one or many child groups. Get the owner's parent combine strategy
        -- for each child group
            -- if union, then make sure this user is set for the child group in user_group_active_members and has the parent set correctly there
            -- if intersection
                -- if user is not a member of the child group's members, make sure this does not exist or is deleted
                -- if user is also member of the child group, then make sure this user is in user_group_active_members (of child) and has the parent set correctly there

    -- todo fill in proc and test
    -- also do deletion proc
    RETURN NEW;
END;
$$ language 'plpgsql';
