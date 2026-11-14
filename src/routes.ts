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
    res.render("pages/home");
  });

  app.get("/contact", (_req: Request, res: Response) => {
    res.render("pages/contact");
  });

  app.get("/posts", (_req: Request, res: Response) => {
    res.render("pages/posts");
  });

  app.get("/photography", (_req: Request, res: Response) => {
    res.render("pages/photography");
  });

  app.get("/toolbox", (_req: Request, res: Response) => {
    res.render("pages/toolbox");
  });

  // Register after middleware must be at end
  registerAfterMiddleware(app);
};
