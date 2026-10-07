import type { Request, Response } from "express";
import { beforeEach, expect, mock, spyOn, test } from "bun:test";
import { EventEmitter } from "node:events";
import { logRequest } from "../logRequest";
import { log } from "../../logger";

const nextFn = mock();

beforeEach(() => {
  mock.restore();
  nextFn.mockClear();
});

const mockInputs = () => ({
  req: {
    path: "/",
    method: "GET",
  } as Request,
  res: Object.assign(new EventEmitter(), {
    statusCode: 200,
  }) as Response,
  next: nextFn,
});

test("logs based on request and response", () => {
  const logInfo = spyOn(log, "info");
  const { req, res, next } = mockInputs();

  logRequest(req, res, next);

  res.emit("finish");

  expect(logInfo.mock.calls.length).toBe(1);
  expect(logInfo.mock.calls[0]?.[0].request.path).toBe("/");
  expect(logInfo.mock.calls[0]?.[0].request.method).toBe("GET");
  expect(logInfo.mock.calls[0]?.[0].response.status).toBe(200);
  expect(logInfo.mock.calls[0]?.[0].response.duration).toBeTypeOf("number");
});

test("calls the provided nextFn", () => {
  const { req, res, next } = mockInputs();

  logRequest(req, res, next);

  expect(nextFn.mock.calls.length).toBe(1);
});
