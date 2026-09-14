import type { NextFunction, Request, Response } from "express";
import { log } from "../logger";

export const errorHandler = (
  err: Error,
  _req: Request,
  res: Response,
  _next: NextFunction,
) => {
  log.error(err.message);

  res.status(500).json({
    message: "Internal Server Error",
    ...(Bun.env.NODE_ENV === "development" && { stack: err.stack }),
  });
};
