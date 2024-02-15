CREATE OR REPLACE FUNCTION recalc_user_group_membership_type()
    RETURNS TRIGGER AS $$
BEGIN
    -- updating group member before saving, we need to decide if this is to be 'working' or 'defined'

    -- get group this belongs to, see if that group a parent to anther group, see if there is a merge strategy (and or)

    -- if user group is parent to any other groups, if parent, for each child group:
        -- if union then add this member to any child group missing this user, make that member's mode working if already defined
        -- if intersection, see if child group already has this, if so change to working instead of defined

    -- see if user group is child to another group, then
        -- if parent has union do nothing but make sure this is in working mode
        -- if parent has intersection, if the parent does not have this as a member, then put it in defined

    -- else if neither parent or child put this in working mode
    -- todo fill in proc and test
    -- todo perhaps this instead fills a new table for working users in the groups, and no status of working for defined
    --  so id group_id, user_id and this gives a nice table for getting perms for stuff
    -- then the the groups
    RETURN NEW;
END;
$$ language 'plpgsql';
