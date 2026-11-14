import { type Express, type Request, type Response } from "express";
import { injectRequestID } from "./middlewares/injectRequestID";
import { logRequest } from "./middlewares/logRequest";
import { errorHandler } from "./middlewares/errorHandler";
import { handleContact } from "./handlers/contact";
import bodyParser from "body-parser";

const registerBeforeMiddleware = (app: Express) => {
  app.use(bodyParser.urlencoded());
  app.use(bodyParser.json());
  app.use(injectRequestID);
  app.use(logRequest);
};

const registerAfterMiddleware = (app: Express) => {
  // Error handler must be last
  app.use(errorHandler);
};

export const registerRoutes = (app: Express) => {
  registerBeforeMiddleware(app);

  app.post("/api/contact", handleContact);

  // Frontend routes
  app.get("/", (_req: Request, res: Response) => {
    res.render("home");
  });

  app.get("/contact", (_req: Request, res: Response) => {
    res.render("contact");
  });

  app.get("/js/htmx.min.js", (_req: Request, res: Response) => {
    res.sendFile(`${__dirname}/views/htmx.min.js`);
  });

  // Register after middleware must be at end
  registerAfterMiddleware(app);
};
