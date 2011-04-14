SELECT tag.name, COUNT(*) AS count FROM (
	(SELECT tag_id FROM tag_idea)
		UNION ALL
		(SELECT tag_id FROM tag_project)
	) AS t
JOIN tag ON (t.tag_id = tag.id)
GROUP BY tag_id
ORDER BY count DESC, name ASC