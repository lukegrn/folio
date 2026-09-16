import { Logger } from "tslog";
import { getConfig } from "./config/config";
import type { Response } from "express";

export const log = new Logger({
  minLevel: getConfig().log.minLevel,
  type: "json",
});

export const logInfoForRequest = (res: Response, body: Object) => {
  log.info({ id: res?.locals?.id, ...body });
};

export const logErrorForRequest = (res: Response, body: Object) => {
  log.error({ id: res?.locals?.id, ...body });
};
