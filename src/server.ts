import express, { Request , Response } from "express";
import mongoose from "mongoose";

const app = express()

app.get('/' , (req: Request , res: Response) => {
    res.send({
        status: 'ok'
    })
})

app.listen(3003 , () => {
    console.log( `server started at http://localhost:${ 3003 }` )
})