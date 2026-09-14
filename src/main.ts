import express, { type Request, type Response } from "express";
import { sql } from "./db/db";
import { logRequest } from "./middlewares/logRequest";
import { log } from "./logger";
import { errorHandler } from "./middlewares/errorHandler";

export const main = () => {
  const app = express();
  const port = 8080;

  app.use(logRequest);

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

  app.get("/err", (_req: Request, _res: Response) => {
    throw new Error();
  });

  // Error handler must be last
  app.use(errorHandler);

  app.listen(port, () => {
    log.info(`Folio listening on port: ${port}`);
  });
};
