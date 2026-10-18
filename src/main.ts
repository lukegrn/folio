import express from "express";
import { log } from "./logger";
import { getConfig } from "./config/config";
import { init as initEmail } from "./email/email";
import { registerRoutes } from "./routes";

export const main = async () => {
  const app = express();

  await initEmail();

  registerRoutes(app);

  app.listen(getConfig().application.port, () => {
    log.info(`Folio listening on port: ${getConfig().application.port}`);
  });
};
