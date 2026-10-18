import type { Express, Request, Response } from "express";
import { sql } from "./db/db";
import { injectRequestID } from "./middlewares/injectRequestID";
import { logRequest } from "./middlewares/logRequest";
import { errorHandler } from "./middlewares/errorHandler";
import { send as sendEmail } from "./email/email";

const registerBeforeMiddleware = (app: Express) => {
  app.use(injectRequestID);
  app.use(logRequest);
};

const registerAfterMiddleware = (app: Express) => {
  // Error handler must be last
  app.use(errorHandler);
};

export const registerRoutes = (app: Express) => {
  registerBeforeMiddleware(app);

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

  app.get("/email/:to", async (req: Request, res: Response) => {
    const success = await sendEmail({
      from: "test@test.com",
      to: String(req.params.to), // placeholder, obv this is bad but is just for testing
      subject: "test",
      message: "test",
      res,
    });

    if (success) {
      res.send({ msg: "Success" });
    } else {
      res.status(500).send({ msg: "Whoops" });
    }
  });

  // Explicit frontend routes
  app.get("/", (_req: Request, res: Response) => {
    res.sendFile(`${__dirname}/views/home.html`);
  });

  app.get("/js/htmx.min.js", (_req: Request, res: Response) => {
    res.sendFile(`${__dirname}/views/htmx.min.js`);
  });

  // Register after middleware must be at end
  registerAfterMiddleware(app);
};
