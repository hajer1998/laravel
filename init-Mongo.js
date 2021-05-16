db.createUser(
    {
        user: "root",
        pwd: "root",
        role: [
            {
                role: "readWrite",
                db : "mongo"
            }
        ]
    }
)
