CREATE OR REPLACE FUNCTION user_group_nudge_owner_member_when()
    RETURNS TRIGGER AS $$
BEGIN
    -- updating group parent stuff, to be run after saving
    -- if the parent got set to a new or another parent (but set to null), then find the owner's membership and update the timestamp
    -- this will start the membership checks

    -- todo fill in proc and test
    RETURN NEW;
END;
$$ language 'plpgsql';
