import express, { type Request, type Response } from "express";
import { sql } from "./db/db";

export const main = () => {
  const app = express();
  const port = 8080;

  app.get("/", (_req: Request, res: Response) => {
    res.send("Hello, world!");
  });

  app.get("/json", (_req: Request, res: Response) => {
    res.send({ msg: "Hello, world!" });
  });

  app.get("/db", async (_req: Request, res: Response) => {
    const info = await sql`
      select
        info
      from temptwo
    `;

    res.send(info);
  });

  app.listen(port, () => {
    console.log(`Folio listening on port: ${port}`);
  });
};
