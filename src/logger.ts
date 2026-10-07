import { Logger } from "tslog";
import { getConfig } from "./config/config";
import type { Response } from "express";

export const log = new Logger({
  minLevel: getConfig().log.minLevel,
  type: process.env.NODE_ENV == "test" ? "hidden" : "json",
});

export const logInfoForRequest = (res: Response, body: object) => {
  log.info({ id: res?.locals?.id, ...body });
};

export const logErrorForRequest = (res: Response, body: object) => {
  log.error({ id: res?.locals?.id, ...body });
};
