CREATE OR REPLACE FUNCTION user_group_stop_parent_recursion()
    RETURNS TRIGGER AS $$
BEGIN
    -- updating group parent, to be run before saving
    -- if not null, see if parent is eventually found again in any child chain
    -- if so throw an error

    -- todo fill in proc and test
    RETURN NEW;
END;
$$ language 'plpgsql';
