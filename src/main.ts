import express, { type Request, type Response } from "express";
import { sql } from "./db/db";
import { logRequest } from "./middlewares/logRequest";
import { log } from "./logger";
import { errorHandler } from "./middlewares/errorHandler";
import { injectRequestID } from "./middlewares/injectRequestID";
import { getConfig } from "./config/config";

export const main = () => {
  const app = express();

  app.use(injectRequestID);
  app.use(logRequest);

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

  // Explicit frontend routes
  app.get("/", (_req: Request, res: Response) => {
    res.sendFile(__dirname + "/views/home.html");
  });

  app.get("/js/htmx.min.js", (_req: Request, res: Response) => {
    res.sendFile(__dirname + "/views/htmx.min.js");
  });

  // Error handler must be last
  app.use(errorHandler);

  app.listen(getConfig().application.port, () => {
    log.info(`Folio listening on port: ${getConfig().application.port}`);
  });
};
