import type { NextFunction, Request, Response } from "express";
import { logErrorForRequest } from "../logger";

export const errorHandler = (
  err: Error,
  _req: Request,
  res: Response,
  _next: NextFunction,
) => {
  logErrorForRequest(res, { message: err.message, id: res?.locals?.id });

  res.status(500).json({
    message: "Internal Server Error",
    ...(Bun.env.NODE_ENV === "development" && { stack: err.stack }),
  });
};
